package gitlabService

import (
	"fmt"
	"github.com/easysoft/z/src/model"
	logUtils "github.com/easysoft/z/src/utils/log"
	"github.com/xanzy/go-gitlab"
	"log"
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
		logUtils.Errorf("gitlab create merge request error %s", err.Error())
		return
	}

	return
}

func ListUser(site model.GitLabSite) (url string) {
	_, _, client := GetClient(site)

	users, _, err := client.Users.ListUsers(&gitlab.ListUsersOptions{})
	if err != nil {
		logUtils.Errorf("gitlab list user error %s", err.Error())
		return
	}
	for _, user := range users {
		log.Println(user.Username, user.Name, user.CreatedAt.Format("2006-01-02"))
	}

	return
}

func GetClient(site model.GitLabSite) (*http.ServeMux, *httptest.Server, *gitlab.Client) {
	mux := http.NewServeMux()
	server := httptest.NewServer(mux)

	client, err := gitlab.NewClient(site.Token, gitlab.WithBaseURL(site.Url))
	if err != nil {
		logUtils.Errorf("connect to gitlab error %s", err.Error())
	}

	return mux, server, client
}
