package scmService

import (
	"fmt"
	fileUtils "github.com/easysoft/z/src/utils/file"
	shellUtils "github.com/easysoft/z/src/utils/shell"
	"path/filepath"
	"strings"
)

const (
	cmdRemote    = "git remote -v"
	cmdGetBranch = "git rev-parse --abbrev-ref HEAD"
	cmdClone     = "git clone %s %s"
	cmdCheckout  = "git checkout %s"
	cmdFork      = "git remote add fork %s"
	cmdFetchAll  = "git fetch --all"
	cmdMerge     = "git merge %s"
)

func MergeLocal(srcBranchDir, distBranchName string) (distBranchDir string, ok bool) {
	repoUrl := GetRemoteUrl(srcBranchDir)
	branchName := GetBranchName(srcBranchDir)

	distBranchDir = GetBrotherDir(srcBranchDir, "dict")
	CheckoutBranch(repoUrl, distBranchName, distBranchDir)

	// merge from same project
	cmdMergeStr := fmt.Sprintf(cmdMerge, branchName)
	shellUtils.ExeWithOutput(cmdMergeStr, distBranchDir)

	// merge from different project
	//cmdForkStr := fmt.Sprintf(cmdFork, repoUrl)
	//shellUtils.ExeWithOutput(cmdForkStr, distBranchDir)
	//cmdFetchAllStr := fmt.Sprintf(cmdFetchAll)
	//_, err := shellUtils.ExeInDir(cmdFetchAllStr, distBranchDir)
	//if err != nil {
	//	logUtils.Errorf("merge failed, error: ", err.Error())
	//}

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
	url = strings.TrimSpace(url)

	return
}

func GetBranchName(dir string) (branch string) {
	outArr := shellUtils.ExeWithOutput(cmdGetBranch, dir)

	if len(outArr) < 1 {
		return
	}

	branch = outArr[0]
	branch = strings.TrimSpace(branch)

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
