package action

import scmService "github.com/easysoft/z/src/service/scm"

func Combine(srcBranchDir, distBranchName string) {
	scmService.CombineLocal(srcBranchDir, distBranchName)
}
