package testing

import (
	scmService "github.com/easysoft/z/src/service/scm"
	logUtils "github.com/easysoft/z/src/utils/log"
	"testing"
)

const ()

func TestDiff(t *testing.T) {
	logUtils.InitLogger()

	out, err := scmService.GetDiffInfo("http://192.168.1.161:51080/root/ci_test_testng.git",
		"ci_branch", "master", "/Users/aaron/ci_test_testng")

	logUtils.Logf("%#v %#v", out, err)
}
