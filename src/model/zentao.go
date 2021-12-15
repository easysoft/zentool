package model

type ZentaoMerge struct {
	MergeResult bool
	MergeMsg    string
	DiffMsg     string

	CIJobId   int
	CIQueueId int
}

type ZentaoBuild struct {
	ServerUrl   string
	ServerToken string
	BuildName   string
}

type ZentaoSite struct {
	BaseUrl  string
	Account  string
	Password string
}
type ZentaoResponse struct {
	Status string
	Data   string
}
