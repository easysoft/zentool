package action

import (
	"fmt"
	scmService "github.com/easysoft/z/src/service/scm"
	logUtils "github.com/easysoft/z/src/utils/log"
)

func Combine(srcBranchDir, distBranchName string) {
	out, distBranchDir, ok := scmService.CombineLocal(srcBranchDir, distBranchName)

	logUtils.Log(fmt.Sprintf("%s, %s, %t", out, distBranchDir, ok))
}
