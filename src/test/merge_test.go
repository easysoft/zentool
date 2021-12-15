package testing

import (
	scmService "github.com/easysoft/z/src/service/scm"
	logUtils "github.com/easysoft/z/src/utils/log"
	"testing"
)

func TestMergeLocal(t *testing.T) {
	logUtils.InitLogger()

	scmService.CombineLocal("/Users/aaron/ci_test_testng_ci_branch", "master")
}
