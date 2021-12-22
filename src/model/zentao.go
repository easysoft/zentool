package model

import constant "github.com/easysoft/z/src/utils/const"

type ZentaoMerge struct {
	MergeStatus bool   `json:"mergeStatus"`
	MergeMsg    string `json:"mergeMsg"`
	DiffMsg     string `json:"diffMsg"`
	UploadMsg   string `json:"uploadMsg"`

	RepoUrl        string `json:"repoUrl"`
	RepoSrcBranch  string `json:"repoSrcBranch"`
	RepoDistBranch string `json:"repoDistBranch"`

	// 可选，仅在执行构建时提供。
	CIJobName string `json:"ciJobName,omitempty"`
	CIQueueId int64  `json:"ciQueueId,omitempty"`
	CIBuildId int64  `json:"ciBuildId,omitempty"`

	// 可选，仅在Z创建MR时提供。
	CreateMrMsg string `json:"createMrMsg,omitempty"`
}

type ZentaoSite struct {
	Url      string `json:"url"`
	Account  string `json:"account"`
	Password string `json:"password"`
}

type ZentaoResponse struct {
	Status string `json:"status"`
	Data   string `json:"data"`
}

type ZentaoMergeResponse struct {
	GitLabMRId int `json:"gitLabMRId"`
	CIBuildId  int `json:"ciBuildId"`
}

type ZentaoRepoResponse struct {
	FileServerUrl      string `json:"fileServerUrl"`
	FileServerAccount  string `json:"fileServerAccount"`
	FileServerPassword string `json:"fileServerPassword"`

	// 可选，仅在执行构建时需要。
	CIServerType    constant.CIServerType `json:"ciServerType,omitempty"`
	CIServerUrl     string                `json:"ciServerUrl,omitempty"`
	CIServerAccount string                `json:"ciServerAccount,omitempty"`
	CIServerToken   string                `json:"ciServerToken,omitempty"`
	CIJobName       string                `json:"ciJobName,omitempty"`

	// 可选，仅在Z创建MR时需要。
	GitLabUrl       string `json:"gitLabUrl,omitempty"`
	GitLabToken     string `json:"gitLabToken,omitempty"`
	GitLabProjectId string `json:"gitLabProjectId,omitempty"`
}
