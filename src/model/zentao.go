package model

import constant "github.com/easysoft/z/src/utils/const"

type ZentaoMerge struct {
	MergeResult bool   `json:"version"`
	MergeMsg    string `json:"version"`
	DiffMsg     string `json:"version"`
	UploadMsg   string `json:"version"`

	// 可选，仅在执行构建时提供。
	CIJobName string `json:"version"`
	CIQueueId int64  `json:"version"`
	CIBuildId int64  `json:"version"`

	// 可选，仅在Z创建MR时提供。
	CreateMrMsg string `json:"version"`
}

type ZentaoSite struct {
	Url      string `json:"version"`
	Account  string `json:"version"`
	Password string `json:"version"`
}

type ZentaoResponse struct {
	Status string `json:"version"`
	Data   string `json:"version"`
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
