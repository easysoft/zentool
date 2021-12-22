package service

import (
	"bytes"
	"encoding/json"
	"github.com/easysoft/z/src/model"
	commonUtils "github.com/easysoft/z/src/utils/common"
	fileUtils "github.com/easysoft/z/src/utils/file"
	i118Utils "github.com/easysoft/z/src/utils/i118"
	logUtils "github.com/easysoft/z/src/utils/log"
	"os"
	"strings"
)

type ConfigService struct {
}

func NewConfigService() *ConfigService {
	return &ConfigService{}
}

func (s *ConfigService) GetConfig() (zentaoSite model.ZentaoSite) {
	//logUtils.Logf("is release %t", commonUtils.IsRelease())
	if !commonUtils.IsRelease() {
		zentaoSite = model.ZentaoSite{
			Url:      "http://192.168.1.161:8080/zentao",
			Account:  "Admin",
			Password: "Admin123#",
		}

		s.TrimConfigField(&zentaoSite)
		//return
	}

	exe := strings.ToLower(os.Args[0])
	file := fileUtils.AbsoluteFile(exe)

	bts := fileUtils.ReadConfFromBin(file)
	bts = bytes.TrimSpace(bts)

	content := strings.TrimSpace(string(bts))
	logUtils.Logf(i118Utils.Sprintf("read_config", file, content))
	json.Unmarshal([]byte(content), &zentaoSite)

	s.TrimConfigField(&zentaoSite)

	return
}

func (s *ConfigService) TrimConfigField(zentaoSite *model.ZentaoSite) {
	zentaoSite.Url = commonUtils.AddSlashForUrl(strings.TrimSpace(zentaoSite.Url))
	zentaoSite.Account = strings.TrimSpace(zentaoSite.Account)
	zentaoSite.Password = strings.TrimSpace(zentaoSite.Password)
}
