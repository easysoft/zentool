package model

type ZentaoMerge struct {
	MergeResult bool
	MergeMsg    string
	DiffMsg     string
	UploadMsg   string

	CIJobName string
	CIQueueId int64
	CIBuildId int64
}

type ZentaoBuild struct {
	CIServerUrl     string
	CIServerAccount string
	CIServerToken   string
	CIJobName       string

	FileServerUrl      string
	FileServerAccount  string
	FileServerPassword string
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
