package testing

import (
	"github.com/easysoft/z/src/model"
	jenkinsService "github.com/easysoft/z/src/service/jenkins"
	scmService "github.com/easysoft/z/src/service/scm"
	zentaoService "github.com/easysoft/z/src/service/zentao"
	fileUtils "github.com/easysoft/z/src/utils/file"
	logUtils "github.com/easysoft/z/src/utils/log"
	"path/filepath"
	"strings"
	"testing"
)

const (
	ZentaoUrl      = "http://127.0.0.1:20080"
	ZentaoAccount  = "admin"
	ZentaoPassword = "P2ssw0rd"
)

func TestGetRepoDefaultBuild(t *testing.T) {
	logUtils.InitLogger()

	zentaoBuild := zentaoService.GetRepoDefaultBuild("http://192.168.1.161:51080/root/ci_test_testng.git", GenSite())

	logUtils.Logf("%#v", zentaoBuild)
}

func TestPostMergeInfo(t *testing.T) {
	logUtils.InitLogger()

	outMerge, outDiff, distBranchDir, ok :=
		scmService.CombineLocal("/Users/aaron/ci_test_testng_ci_branch", "master")

	zipFile := filepath.Join(filepath.Dir(distBranchDir), "result.zip")
	fileUtils.ZipFiles(zipFile, distBranchDir)

	merger := model.ZentaoMerge{
		MergeResult: ok,
		MergeMsg:    strings.Join(outMerge, "\n"),
		DiffMsg:     strings.Join(outDiff, "\n"),
	}

	zentaoBuild := zentaoService.GetRepoDefaultBuild("http://192.168.1.161:51080/root/ci_test_testng.git", GenSite())

	files := []string{""}
	params := map[string]string{"account": zentaoBuild.FileServerAccount, "password": zentaoBuild.FileServerPassword}
	uploadResult, uploadErr := fileUtils.Upload(zentaoBuild.FileServerUrl, files, params)
	merger.UploadMsg = uploadErr.Error()

	if ok && uploadErr == nil {
		jenkinsSite := model.JenkinsSite{
			Url: zentaoBuild.CIServerUrl, Account: zentaoBuild.CIServerAccount, Token: zentaoBuild.CIServerToken}
		queueId, buildId := jenkinsService.BuildJob(zentaoBuild.CIJobName, uploadResult.FileDir, jenkinsSite, true)

		merger.CIJobName = zentaoBuild.CIJobName
		merger.CIQueueId = queueId
		merger.CIBuildId = buildId
	}

	zentaoService.PostMergeInfo(merger, GenSite())
	logUtils.Logf("%#v", zentaoBuild)
}

func GenSite() (site model.ZentaoSite) {
	site = model.ZentaoSite{
		Url:      ZentaoUrl,
		Account:  ZentaoAccount,
		Password: ZentaoPassword,
	}

	return
}
