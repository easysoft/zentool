package action

import (
	"fmt"
	"github.com/easysoft/z/src/model"
	"github.com/easysoft/z/src/service"
	constant "github.com/easysoft/z/src/utils/const"
	fileUtils "github.com/easysoft/z/src/utils/file"
	i118Utils "github.com/easysoft/z/src/utils/i118"
	logUtils "github.com/easysoft/z/src/utils/log"
	"path/filepath"
	"strings"
)

type MergeAction struct {
	ConfigService  *service.ConfigService  `inject:""`
	ZentaoService  *service.ZentaoService  `inject:""`
	ScmService     *service.ScmService     `inject:""`
	GitLabService  *service.GitLabService  `inject:""`
	JenkinsService *service.JenkinsService `inject:""`
}

func NewMergeAction() *MergeAction {
	return &MergeAction{}
}

func (a *MergeAction) Merge(srcBranchDir, distBranchName string) (resp model.ZentaoMergeResponse, err error) {
	if srcBranchDir == "" {
		srcBranchDir = fileUtils.GetWorkDir()
	}

	conf, err := a.ConfigService.GetConfig()
	if err != nil {
		logUtils.Errorf(i118Utils.Sprintf("read_config_fail"))
		return
	}

	resp, err = a.MergeAllSteps(srcBranchDir, distBranchName, conf, false, false, false)

	return
}

func (a *MergeAction) MergeAllSteps(srcBranchDir, distBranchName string,
	zentaoSite model.ZentaoSite, execCIBuild, waitBuildCompleted, createGitLabMr bool) (

	resp model.ZentaoMergeResponse, err error) {

	outMerge, outDiff, repoUrl, srcBranchName, srcBranchNameRemote, distBranchDir, errCombine :=
		a.ScmService.CombineCodes(srcBranchDir, distBranchName)

	if errCombine != nil {
		logUtils.Errorf(errCombine.Error())
		return
	}

	mergerInfo := model.ZentaoMerge{
		MergeStatus: errCombine == nil,
		MergeMsg:    strings.Join(outMerge, "\n"),
		DiffMsg:     strings.Join(outDiff, "\n"),

		RepoUrl:             repoUrl,
		RepoSrcBranch:       srcBranchName,
		RepoSrcBranchRemote: srcBranchNameRemote,
		RepoDistBranch:      distBranchName,
	}

	zentaoBuild, errGetRepo := a.ZentaoService.GetRepByUrl(repoUrl, zentaoSite)
	if errGetRepo != nil {
		logUtils.Errorf(i118Utils.Sprintf("get_repo_default_build_fail", errGetRepo.Error()))
	}

	var uploadResult model.UploadResponse
	var uploadErr error

	// upload file
	if errGetRepo == nil && errCombine == nil {
		zipFile := filepath.Join(fileUtils.GetParent(distBranchDir), "result.zip")
		fileUtils.ZipFiles(zipFile, distBranchDir)

		files := []string{zipFile}
		params := map[string]string{"account": zentaoBuild.FileServerAccount, "password": zentaoBuild.FileServerPassword}
		uploadResult, uploadErr = fileUtils.Upload(zentaoBuild.FileServerUrl, files, params)

		msg := "success"
		if uploadErr != nil {
			msg = uploadErr.Error()
			logUtils.Errorf(i118Utils.Sprintf("upload_combined_code_fail", uploadErr.Error()))
		}

		mergerInfo.UploadPath = uploadResult.FilePath
		mergerInfo.UploadMsg = fmt.Sprintf("status %t, %s。", uploadResult.Status, msg)
	}

	// exec build on CI platform
	if execCIBuild && errGetRepo == nil && errCombine == nil && uploadErr == nil {
		if zentaoBuild.CIServerType == constant.Jenkins {
			jenkinsSite := model.JenkinsSite{
				Url: zentaoBuild.CIServerUrl, Account: zentaoBuild.CIServerAccount, Token: zentaoBuild.CIServerToken}

			queueId, buildId, errBuildJob := a.JenkinsService.BuildJob(zentaoBuild.CIJobName, uploadResult.FilePath, jenkinsSite, waitBuildCompleted)

			mergerInfo.CIJobName = zentaoBuild.CIJobName
			mergerInfo.CIQueueId = queueId
			mergerInfo.CIBuildId = buildId

			if errBuildJob != nil {
				logUtils.Errorf(i118Utils.Sprintf("build_jenkins_job_fail", errBuildJob.Error()))
			}
		}
	}

	// create MR in gitlab
	if createGitLabMr && errGetRepo == nil {
		gitlabSite := model.GitLabSite{Url: zentaoBuild.GitLabUrl, Token: zentaoBuild.GitLabToken}
		mr, errCreateMr := a.GitLabService.CreateMr(zentaoBuild.GitLabProjectId, srcBranchName, srcBranchNameRemote, distBranchName, gitlabSite)

		if errCreateMr != nil {
			mergerInfo.CreateMrMsg = errCreateMr.Error()
			logUtils.Errorf(i118Utils.Sprintf("create_gitlab_mr_fail", errCreateMr.Error()))

		} else {
			mergerInfo.CreateMrMsg = fmt.Sprintf("success to create mr %s", mr.Title)
		}
	}

	resp, err = a.ZentaoService.SubmitMergeInfo(mergerInfo, zentaoSite)

	return
}
