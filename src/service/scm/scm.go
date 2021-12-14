package scmService

import (
	"fmt"
	fileUtils "github.com/easysoft/zentaoatf/src/utils/file"
	logUtils "github.com/easysoft/zentaoatf/src/utils/log"
	shellUtils "github.com/easysoft/zentaoatf/src/utils/shell"
	"path/filepath"
	"strings"
)

const (
	cmdRemote = "git remote -v"
	cmdGetBranch = "git rev-parse --abbrev-ref HEAD"
	cmdClone = "git clone %s %s"
	cmdCheckout = "git checkout %s"
	cmdFork = "git remote add fork %s"
	cmdFetchAll = "git fetch --all"
	cmdMerge = "git merge fork/$sourceBranch"
)

func MergeLocal(srcDir, distBranch string) (distDir string, ok bool) {
	repoUrl := GetRemoteUrl(srcDir)
	//branchName := GetBranchName(srcDir)

	distDir = GetBrotherDir(srcDir, "dict")
	CheckoutBranch(repoUrl, distBranch, distDir)

	cmdForkStr := fmt.Sprintf(cmdFork, repoUrl)
	shellUtils.ExeWithOutput(cmdForkStr, distDir)

	cmdFetchAllStr := fmt.Sprintf(cmdFetchAll)
	_, err := shellUtils.ExeInDir(cmdFetchAllStr, distDir)
	if err != nil {
		logUtils.Errorf("merge failed, error: ", err.Error())
	}

	return
}

func GetRemoteUrl(dir string) (url string) {
	outArr := shellUtils.ExeWithOutput(cmdRemote, dir)

	if len(outArr) < 1 {
		return
	}

	line := outArr[0]
	fields := strings.Split(line, "\t")
	if len(outArr) < 2 {
		return
	}

	section := strings.Split(fields[1], " ")
	url = section[0]

	return
}

func GetBranchName(dir string) (branch string) {
	outArr := shellUtils.ExeWithOutput(cmdGetBranch, dir)

	if len(outArr) < 1 {
		return
	}

	branch = outArr[0]

	return
}

func CheckoutBranch(repoUrl, distBranch, distDir string) {
	cmdCloneStr := fmt.Sprintf(cmdClone, repoUrl, distDir)
	shellUtils.ExeWithOutput(cmdCloneStr, "")

	cmdCheckoutStr := fmt.Sprintf(cmdCheckout, distBranch)
	shellUtils.ExeWithOutput(cmdCheckoutStr, distDir)

	return
}

func GetBrotherDir(base, name string) (dir string) {
	parentDir := filepath.Dir(base)

	dir = filepath.Join(parentDir, dir)
	dir = fileUtils.AbsolutePath(dir)

	return
}