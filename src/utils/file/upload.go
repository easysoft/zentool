package fileUtils

import (
	"bytes"
	"encoding/json"
	"github.com/easysoft/z/src/model"
	"github.com/easysoft/z/src/utils/i118"
	"github.com/easysoft/z/src/utils/log"
	"github.com/easysoft/z/src/utils/vari"
	"io"
	"io/ioutil"
	"mime/multipart"
	"net/http"
	"os"
)

func Upload(url string, files []string, extraParams map[string]string) (uploadResult model.UploadResponse, err error) {
	bodyBuffer := &bytes.Buffer{}
	bodyWriter := multipart.NewWriter(bodyBuffer)

	for _, file := range files {
		fw, _ := bodyWriter.CreateFormFile("file", file)
		f, _ := os.Open(file)
		defer f.Close()
		io.Copy(fw, f)
	}

	for key, value := range extraParams {
		_ = bodyWriter.WriteField(key, value)
	}

	contentType := bodyWriter.FormDataContentType()
	bodyWriter.Close()

	resp, err := http.Post(url, contentType, bodyBuffer)
	if err != nil {
		return
	}

	bodyStr, err := ioutil.ReadAll(resp.Body)
	defer resp.Body.Close()
	if vari.Verbose {
		logUtils.Log(i118Utils.Sprintf("server_return", logUtils.ConvertUnicode(bodyStr)))
	}

	if err != nil {
		logUtils.Logf(i118Utils.Sprintf("read_upload_response_error", err.Error()))
		return
	}

	err = json.Unmarshal(bodyStr, &uploadResult)
	if err != nil {
		logUtils.Logf(i118Utils.Sprintf("parse_upload_response_error", err.Error()))
		return
	}

	return
}
