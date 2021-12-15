package action

import (
	"fmt"
	"github.com/easysoft/z/src/model"
	gitlabService "github.com/easysoft/z/src/service/gitlab"
	jenkinsService "github.com/easysoft/z/src/service/jenkins"
	scmService "github.com/easysoft/z/src/service/scm"
	zentaoService "github.com/easysoft/z/src/service/zentao"
	fileUtils "github.com/easysoft/z/src/utils/file"
	logUtils "github.com/easysoft/z/src/utils/log"
	"path/filepath"
	"strings"
)

func Merge(srcBranchDir, distBranchName string, zentaoSite model.ZentaoSite) (
	resp model.ZentaoMergeResponse, err error) {
	return MergeAllSteps(srcBranchDir, distBranchName, zentaoSite, false, false, false)
}

func MergeAllSteps(srcBranchDir, distBranchName string, zentaoSite model.ZentaoSite, createBuild, waitBuildCompleted, createMr bool) (
	resp model.ZentaoMergeResponse, err error) {
	outMerge, outDiff, srcBranchName, distBranchDir, errCombine :=
		scmService.CombineLocal(srcBranchDir, distBranchName)

	zipFile := filepath.Join(filepath.Dir(distBranchDir), "result.zip")
	fileUtils.ZipFiles(zipFile, distBranchDir)

	mergerInfo := model.ZentaoMerge{
		MergeResult: errCombine == nil,
		MergeMsg:    strings.Join(outMerge, "\n"),
		DiffMsg:     strings.Join(outDiff, "\n"),
	}

	zentaoBuild := zentaoService.GetRepoDefaultBuild("http://192.168.1.161:51080/root/ci_test_testng.git", zentaoSite)

	files := []string{""}
	params := map[string]string{"account": zentaoBuild.FileServerAccount, "password": zentaoBuild.FileServerPassword}
	uploadResult, uploadErr := fileUtils.Upload(zentaoBuild.FileServerUrl, files, params)
	mergerInfo.UploadMsg = uploadErr.Error()

	if createBuild && errCombine == nil && uploadErr == nil {
		jenkinsSite := model.JenkinsSite{
			Url: zentaoBuild.CIServerUrl, Account: zentaoBuild.CIServerAccount, Token: zentaoBuild.CIServerToken}
		queueId, buildId := jenkinsService.BuildJob(zentaoBuild.CIJobName, uploadResult.FileDir, jenkinsSite, waitBuildCompleted)

		mergerInfo.CIJobName = zentaoBuild.CIJobName
		mergerInfo.CIQueueId = queueId
		mergerInfo.CIBuildId = buildId
	}

	if createMr {
		gitlabSite := model.GitLabSite{Url: zentaoBuild.GitLabUrl, Token: zentaoBuild.GitLabToken}
		mr, err := gitlabService.CreateMr(zentaoBuild.GitLabProjectId, srcBranchName, distBranchName, gitlabSite)

		if err != nil {
			mergerInfo.CreateMrMsg = err.Error()
		} else {
			mergerInfo.CreateMrMsg = fmt.Sprintf("success to create mr %s", mr.Title)
		}
	}

	resp, err = zentaoService.PostMergeInfo(mergerInfo, zentaoSite)
	logUtils.Logf("zentao return: mergeId=%d, buildId=%d", resp.MRId, resp.BuildId)

	return
}
