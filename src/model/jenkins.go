package model

type JenkinsSite struct {
	Url     string `json:"url"`
	Account string `json:"account"`
	Token   string `json:"token"`
}
