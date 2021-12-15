package jenkinsService

import (
	"context"
	"github.com/bndr/gojenkins"
	"github.com/easysoft/z/src/model"
	logUtils "github.com/easysoft/z/src/utils/log"
	"time"
)

const ()

func BuildJob(jobName, workDir string, site model.JenkinsSite, waitBuildComplete bool) (queueId, buildId int64) {
	ctx := context.Background()
	jenkins := gojenkins.CreateJenkins(nil, site.Url, site.Account, site.Token)
	_, err := jenkins.Init(ctx)
	if err != nil {
		logUtils.Errorf("connect to jenkins error %s", err.Error())
		return
	}

	job, err := jenkins.GetJob(ctx, jobName)
	if err != nil {
		logUtils.Errorf("jenkins get job error %s", err.Error())
		return
	}

	params := map[string]string{
		workDir: workDir,
	}
	queueId, err = jenkins.BuildJob(ctx, job.GetName(), params)
	if err != nil {
		logUtils.Errorf("jenkins build job error %s", err.Error())
		return
	}

	build, err := jenkins.GetBuild(ctx, job.GetName(), queueId)
	if err != nil {
		logUtils.Errorf("jenkins get build error %s", err.Error())
		return
	}
	buildId = build.GetBuildNumber()

	logBuildStatus(*job, queueId, *build)

	if waitBuildComplete {
		for build.IsRunning(ctx) {
			time.Sleep(5000 * time.Millisecond)
			build.Poll(ctx)

			logBuildStatus(*job, queueId, *build)
		}
	}

	return
}

func logBuildStatus(job gojenkins.Job, queueId int64, build gojenkins.Build) {
	logUtils.Logf("job %s, queue %d, build %d-%s with result: %v\n",
		job.GetName(), queueId,
		build.GetBuildNumber(), build.Info().DisplayName, build.GetResult())

	return
}
