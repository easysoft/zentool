package testing

import (
	"github.com/easysoft/z/src/action"
	"github.com/easysoft/z/src/model"
	constant "github.com/easysoft/z/src/utils/const"
	i118Utils "github.com/easysoft/z/src/utils/i118"
	logUtils "github.com/easysoft/z/src/utils/log"
	"testing"
)

const (
	ZentaoUrl      = "http://127.0.0.1:20080"
	ZentaoAccount  = "admin"
	ZentaoPassword = "P2ssw0rd"
)

func TestMerge(t *testing.T) {
	logUtils.InitLogger()
	i118Utils.InitI118(constant.LanguageZH)

	site := model.ZentaoSite{
		Url:      ZentaoUrl,
		Account:  ZentaoAccount,
		Password: ZentaoPassword,
	}
	action.Merge("/Users/aaron/ci_test_testng_ci_branch", "master", site, true)
}
