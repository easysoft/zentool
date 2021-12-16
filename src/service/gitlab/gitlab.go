package gitlabService

import (
	"fmt"
	"github.com/easysoft/z/src/model"
	i118Utils "github.com/easysoft/z/src/utils/i118"
	logUtils "github.com/easysoft/z/src/utils/log"
	"github.com/xanzy/go-gitlab"
	"net/http"
	"net/http/httptest"
)

const ()

func CreateMr(projectId, srcBranch, distBranch string, site model.GitLabSite) (mr *gitlab.MergeRequest, err error) {
	_, _, client := GetClient(site)

	opt := gitlab.CreateMergeRequestOptions{
		SourceBranch: gitlab.String(srcBranch),
		TargetBranch: gitlab.String(distBranch),
		Title:        gitlab.String(fmt.Sprintf("%s to %s, created by zentao", srcBranch, distBranch)),
	}

	mr, _, err = client.MergeRequests.CreateMergeRequest(projectId, &opt)
	if err != nil {
		logUtils.Errorf(i118Utils.Sprintf("create_mr_error", err.Error()))
		return
	}

	return
}

func GetClient(site model.GitLabSite) (*http.ServeMux, *httptest.Server, *gitlab.Client) {
	mux := http.NewServeMux()
	server := httptest.NewServer(mux)

	client, err := gitlab.NewClient(site.Token, gitlab.WithBaseURL(site.Url))
	if err != nil {
		logUtils.Errorf(i118Utils.Sprintf("connect_gitlab_error", err.Error()))
	}

	return mux, server, client
}
