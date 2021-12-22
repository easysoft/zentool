package model

type UploadResponse struct {
	Status   bool   `json:"status"`
	FilePath string `json:"filePath"`
}
