package action

import (
	"github.com/easysoft/z/src/model"
	jenkinsService "github.com/easysoft/z/src/service/jenkins"
	scmService "github.com/easysoft/z/src/service/scm"
	zentaoService "github.com/easysoft/z/src/service/zentao"
	fileUtils "github.com/easysoft/z/src/utils/file"
	"path/filepath"
	"strings"
)

func Merge(srcBranchDir, distBranchName string, zentaoSite model.ZentaoSite, waitBuildCompleted bool) {
	outMerge, outDiff, distBranchDir, ok :=
		scmService.CombineLocal(srcBranchDir, distBranchName)

	zipFile := filepath.Join(filepath.Dir(distBranchDir), "result.zip")
	fileUtils.ZipFiles(zipFile, distBranchDir)

	merger := model.ZentaoMerge{
		MergeResult: ok,
		MergeMsg:    strings.Join(outMerge, "\n"),
		DiffMsg:     strings.Join(outDiff, "\n"),
	}

	zentaoBuild := zentaoService.GetRepoDefaultBuild("http://192.168.1.161:51080/root/ci_test_testng.git", zentaoSite)

	files := []string{""}
	params := map[string]string{"account": zentaoBuild.FileServerAccount, "password": zentaoBuild.FileServerPassword}
	uploadResult, uploadErr := fileUtils.Upload(zentaoBuild.FileServerUrl, files, params)
	merger.UploadMsg = uploadErr.Error()

	if ok && uploadErr == nil {
		jenkinsSite := model.JenkinsSite{
			Url: zentaoBuild.CIServerUrl, Account: zentaoBuild.CIServerAccount, Token: zentaoBuild.CIServerToken}
		queueId, buildId := jenkinsService.BuildJob(zentaoBuild.CIJobName, uploadResult.FileDir, jenkinsSite, waitBuildCompleted)

		merger.CIJobName = zentaoBuild.CIJobName
		merger.CIQueueId = queueId
		merger.CIBuildId = buildId
	}

	zentaoService.PostMergeInfo(merger, zentaoSite)
}
