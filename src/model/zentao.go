package model

type ZentaoMerge struct {
	MergeResult bool
	MergeMsg    string
	DiffMsg     string
	UploadMsg   string
	CreateMrMsg string // 可选

	CIJobName string
	CIQueueId int64
	CIBuildId int64
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
	MRId    int
	BuildId int
}

type ZentaoRepoResponse struct {
	CIServerUrl     string
	CIServerAccount string
	CIServerToken   string
	CIJobName       string

	FileServerUrl      string
	FileServerAccount  string
	FileServerPassword string

	// 可选，仅在Z创建MR时需要。
	GitLabUrl       string
	GitLabToken     string
	GitLabProjectId string
}
