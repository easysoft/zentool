package service

import (
	"context"
	"github.com/bndr/gojenkins"
	"github.com/easysoft/z/src/model"
	i118Utils "github.com/easysoft/z/src/utils/i118"
	logUtils "github.com/easysoft/z/src/utils/log"
	"time"
)

type JenkinsService struct {
}

func NewJenkinsService() *JenkinsService {
	return &JenkinsService{}
}

func (s *JenkinsService) BuildJob(jobName, workDir string, site model.JenkinsSite, waitBuildComplete bool) (
	queueId, buildId int64, err error) {

	ctx := context.Background()
	jenkins := gojenkins.CreateJenkins(nil, site.Url, site.Account, site.Token)
	_, err = jenkins.Init(ctx)
	if err != nil {
		logUtils.Errorf(i118Utils.Sprintf("connect_jenkins_error", err.Error()))
		return
	}

	job, err := jenkins.GetJob(ctx, jobName)
	if err != nil {
		logUtils.Errorf(i118Utils.Sprintf("get_jenkins_job_error", err.Error()))
		return
	}

	params := map[string]string{
		workDir: workDir,
	}
	queueId, err = jenkins.BuildJob(ctx, job.GetName(), params)
	if err != nil {
		logUtils.Errorf(i118Utils.Sprintf("exec_jenkins_job_error", err.Error()))
		return
	}

	build, err := jenkins.GetBuild(ctx, job.GetName(), queueId)
	if err != nil {
		logUtils.Errorf(i118Utils.Sprintf("get_jenkins_build_error", err.Error()))
		return
	}
	buildId = build.GetBuildNumber()

	s.logBuildStatus(*job, queueId, *build)

	if waitBuildComplete {
		for build.IsRunning(ctx) {
			time.Sleep(5000 * time.Millisecond)
			build.Poll(ctx)

			s.logBuildStatus(*job, queueId, *build)
		}
	}

	return
}

func (s *JenkinsService) logBuildStatus(job gojenkins.Job, queueId int64, build gojenkins.Build) {
	logUtils.Logf(i118Utils.Sprintf("jenkins_task_info",
		job.GetName(), queueId,
		build.GetBuildNumber(), build.Info().DisplayName, build.GetResult()))

	return
}
