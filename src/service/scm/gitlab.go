package scmService

import (
	"github.com/xanzy/go-gitlab"
	"log"
)

const ()

func Test() (url string) {
	git, err := gitlab.NewClient("xUBrqqoYAx-i43Sn4XGK",
		gitlab.WithBaseURL("http://192.168.1.161:51080//api/v4"))
	if err != nil {
		log.Printf("Failed to create client, error: %v", err)
	}

	users, _, err := git.Users.ListUsers(&gitlab.ListUsersOptions{})
	if err != nil {
		log.Printf("Failed to ListUsers, error: %v", err)
	}
	log.Println(users)

	opt := &gitlab.ListProjectsOptions{Search: gitlab.String("svanharmelen")}
	projects, _, err := git.Projects.ListProjects(opt)
	if err != nil {
		log.Printf("Failed to ListProjectsOptions, error: %v", err)
	}

	log.Println(projects)

	return
}
