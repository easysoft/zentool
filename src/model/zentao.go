package model

import constant "github.com/easysoft/z/src/utils/const"

type ZentaoMerge struct {
	MergeResult bool
	MergeMsg    string
	DiffMsg     string
	UploadMsg   string

	// 可选，仅在执行构建时提供。
	CIJobName string
	CIQueueId int64
	CIBuildId int64

	// 可选，仅在Z创建MR时提供。
	CreateMrMsg string
}

type ZentaoSite struct {
	Url      string
	Account  string
	Password string
}

type ZentaoResponse struct {
	Status string
	Data   string
}

type ZentaoMergeResponse struct {
	GitLabMRId int
	CIBuildId  int
}

type ZentaoRepoResponse struct {
	FileServerUrl      string
	FileServerAccount  string
	FileServerPassword string

	// 可选，仅在执行构建时需要。
	CIServerType    constant.CIServerType
	CIServerUrl     string
	CIServerAccount string
	CIServerToken   string
	CIJobName       string

	// 可选，仅在Z创建MR时需要。
	GitLabUrl       string
	GitLabToken     string
	GitLabProjectId string
}
