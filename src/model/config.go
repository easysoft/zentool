package model

type Config2 struct {
	Version  float64 `json:"version"`
	Language string  `json:"language"`

	Url      string `json:"url"`
	Account  string `json:"account"`
	Password string `json:"password"`
}
