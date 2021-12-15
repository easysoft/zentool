package model

type ZentaoBuild struct {
	ServerUrl   string
	ServerToken string
	BuildName   string
}

type ZentaoResponse struct {
	Status string
	Data   string
}
