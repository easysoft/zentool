package action

import scmService "github.com/easysoft/zentaoatf/src/service/scm"

func Merge(targetBranch string) {
	scmService.MergeLocal(targetBranch)
}
