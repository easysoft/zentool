package scmService

import (
	"errors"
	"fmt"
	fileUtils "github.com/easysoft/z/src/utils/file"
	logUtils "github.com/easysoft/z/src/utils/log"
	shellUtils "github.com/easysoft/z/src/utils/shell"
	"path/filepath"
	"strings"
)

const (
	KeywordConflict = "CONFLICT"

	cmdRemote    = "git remote -v"
	cmdGetBranch = "git rev-parse --abbrev-ref HEAD"
	cmdClone     = "git clone %s %s"
	cmdCheckout  = "git checkout %s"
	cmdFork      = "git remote add fork %s"
	cmdFetchAll  = "git fetch --all"
	cmdMerge     = "git merge %s"
)

func CombineLocal(srcBranchDir, distBranchName string) (outMerge, outDiff []string, srcBranchName, distBranchDir string, err error) {
	repoUrl, label := GetRemoteUrl(srcBranchDir)
	srcBranchName, err = GetBranchName(srcBranchDir)
	if err != nil {
		return
	}

	distBranchDir = GetBrotherDir(srcBranchDir, distBranchName)
	outMerge, err = CheckoutBranch(repoUrl, distBranchName, distBranchDir)
	if err != nil {
		return
	}

	// merge from same project
	outMerge, outDiff, err = MergeFromSameProject(label, srcBranchName, distBranchDir)
	if err != nil {
		return
	}

	return

	// merge from different project
	//cmdForkStr := fmt.Sprintf(cmdFork, repoUrl)
	//shellUtils.ExeWithOutput(cmdForkStr, distBranchDir)
	//cmdFetchAllStr := fmt.Sprintf(cmdFetchAll)
	//_, err := shellUtils.ExeWithOutput(cmdFetchAllStr, distBranchDir)
	//if err != nil {
	//	logUtils.Errorf("merge failed, error: ", err.Error())
	//}
}

func GetRemoteUrl(dir string) (url, label string) {
	out, err := shellUtils.ExeWithOutput(cmdRemote, dir)
	if err != nil {
		logUtils.Errorf("failed to execute cmd %s., error: %s", cmdGetBranch, err.Error())
	}

	if len(out) < 1 {
		return
	}

	line := out[0]
	fields := strings.Split(line, "\t")
	if len(out) < 2 {
		return
	}

	label = fields[0]

	section := strings.Split(fields[1], " ")
	url = section[0]
	url = strings.TrimSpace(url)

	return
}

func GetBranchName(dir string) (branch string, err error) {
	out, err := shellUtils.ExeWithOutput(cmdGetBranch, dir)
	if err != nil {
		logUtils.Errorf("failed to execute cmd %s., error: %s", cmdGetBranch, err.Error())
	}

	if len(out) < 1 {
		return
	}

	branch = out[0]
	branch = strings.TrimSpace(branch)

	return
}

func CheckoutBranch(repoUrl, distBranch, distDir string) (out []string, err error) {
	fileUtils.RmDir(distDir)

	cmdCloneStr := fmt.Sprintf(cmdClone, repoUrl, distDir)
	out, err = shellUtils.ExeWithOutput(cmdCloneStr, "")
	if err != nil {
		logUtils.Errorf("failed to execute cmd %s., error: %s", cmdCloneStr, err.Error())
	}

	cmdCheckoutStr := fmt.Sprintf(cmdCheckout, distBranch)
	out, err = shellUtils.ExeWithOutput(cmdCheckoutStr, distDir)
	if err != nil {
		logUtils.Errorf("failed to execute cmd %s., error: %s", cmdCheckoutStr, err.Error())
	}

	return
}

func MergeFromSameProject(label string, branchName string, distBranchDir string) (outMerge, outDiff []string, err error) {
	cmdMergeStr := fmt.Sprintf(cmdMerge, fmt.Sprintf("%s/%s", label, branchName))
	outMerge, err = shellUtils.ExeWithOutput(cmdMergeStr, distBranchDir)
	if err != nil {
		logUtils.Errorf("failed to execute cmd %s., error: %s", cmdMergeStr, err.Error())
		return
	}

	msg := strings.Join(outMerge, "\n")
	if strings.Index(msg, "conflict") > -1 {
		err = errors.New("Merge Conflict: " + msg)
	}

	outDiff = GetDiffInfo(label, branchName, distBranchDir)

	return
}

func GetDiffInfo(label string, branchName string, distBranchDir string) (out []string) {

	return
}

func GetBrotherDir(base, name string) (dir string) {
	parentDir := filepath.Dir(base)

	dir = filepath.Join(parentDir, name)
	dir = fileUtils.AbsolutePath(dir)

	return
}
