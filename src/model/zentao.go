package model

import constant "github.com/easysoft/z/src/utils/const"

type ZentaoMerge struct {
	MergeResult bool   `json:"mergeResult"`
	MergeMsg    string `json:"mergeMsg"`
	DiffMsg     string `json:"diffMsg"`
	UploadMsg   string `json:"uploadMsg"`

	// 可选，仅在执行构建时提供。
	CIJobName string `json:"ciJobName"`
	CIQueueId int64  `json:"ciQueueId"`
	CIBuildId int64  `json:"ciBuildId"`

	// 可选，仅在Z创建MR时提供。
	CreateMrMsg string `json:"createMrMsg"`
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
	CIServerType    constant.CIServerType `json:"ciServerType"`
	CIServerUrl     string                `json:"ciServerUrl"`
	CIServerAccount string                `json:"ciServerAccount"`
	CIServerToken   string                `json:"ciServerToken"`
	CIJobName       string                `json:"ciJobName"`

	// 可选，仅在Z创建MR时需要。
	GitLabUrl       string `json:"gitLabUrl"`
	GitLabToken     string `json:"gitLabToken"`
	GitLabProjectId string `json:"gitLabProjectId"`
}
