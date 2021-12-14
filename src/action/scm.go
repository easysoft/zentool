package action

import scmService "github.com/easysoft/z/src/service/scm"

func Merge(targetBranch string) {
	scmService.CombineLocal(targetBranch)
}
