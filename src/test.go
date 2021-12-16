package main

import (
	"encoding/json"
	"github.com/easysoft/z/src/model"
	constant "github.com/easysoft/z/src/utils/const"
	i118Utils "github.com/easysoft/z/src/utils/i118"
	logUtils "github.com/easysoft/z/src/utils/log"
)

func main() {
	logUtils.InitLogger()
	i118Utils.InitI118(constant.LanguageZH)

	conf := model.ZentaoSite{
		Url:      "http://127.0.0.1:20080",
		Account:  "admin",
		Password: "P2ssw0rd",
	}

	str, _ := json.Marshal(conf)
	logUtils.Log(i118Utils.Sprintf("read_config", string(str), ""))
}
