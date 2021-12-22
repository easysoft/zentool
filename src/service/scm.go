package service

import (
	"errors"
	"fmt"
	fileUtils "github.com/easysoft/z/src/utils/file"
	i118Utils "github.com/easysoft/z/src/utils/i118"
	logUtils "github.com/easysoft/z/src/utils/log"
	shellUtils "github.com/easysoft/z/src/utils/shell"
	"path/filepath"
	"strings"
)

const (
	KeywordConflict = "CONFLICT"

	cmdRemote          = "git remote -v"
	cmdGetBranch       = "git rev-parse --abbrev-ref HEAD"
	cmdGetRemoteBranch = "git config --list | grep -v grep | grep branch.%s.merge"
	cmdClone           = "git clone %s %s"
	cmdCheckout        = "git checkout %s"
	cmdMerge           = "git merge %s"
	cmdDiff            = "git diff %s %s"

	//cmdFork      = "git remote add fork %s"
	//cmdFetchAll  = "git fetch --all"
)

type ScmService struct {
}

func NewScmService() *ScmService {
	return &ScmService{}
}

func (s *ScmService) CombineCodesLocally(srcBranchDir, distBranchName string) (
	outMerge, outDiff []string, repoUrl, srcBranchName, srcBranchNameRemote, distBranchDir string, errCombine error) {

	var err error

	srcBranchDir = fileUtils.AbsoluteFile(srcBranchDir)

	var label string
	repoUrl, label = GetRemoteUrl(srcBranchDir)
	srcBranchName, srcBranchNameRemote, err = s.GetBranchName(srcBranchDir)
	if err != nil {
		return
	}

	distBranchDir = s.GetBrotherDir(srcBranchDir, distBranchName)
	outMerge, err = s.CheckoutBranch(repoUrl, distBranchName, distBranchDir)
	if err != nil {
		return
	}

	outMerge, outDiff, errCombine = MergeFromSameProject(label, srcBranchName, distBranchDir)

	outDiff, err = s.GetDiffInfo(repoUrl, srcBranchName, distBranchName, distBranchDir)

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
		logUtils.Errorf(i118Utils.Sprintf("fail_to_execute_cmd", cmdRemote, err.Error()))
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

func (s *ScmService) CheckoutBranch(repoUrl, distBranch, distDir string) (out []string, err error) {
	fileUtils.RmDir(distDir)

	cmdCloneStr := fmt.Sprintf(cmdClone, repoUrl, distDir)
	out, err = shellUtils.ExeWithOutput(cmdCloneStr, "")
	if err != nil {
		logUtils.Errorf(i118Utils.Sprintf("fail_to_execute_cmd", cmdCloneStr, err.Error()))
	}

	cmdCheckoutStr := fmt.Sprintf(cmdCheckout, distBranch)
	out, err = shellUtils.ExeWithOutput(cmdCheckoutStr, distDir)
	if err != nil {
		logUtils.Errorf(i118Utils.Sprintf("fail_to_execute_cmd", cmdCheckoutStr, err.Error()))
	}

	return
}

func MergeFromSameProject(label string, srcBranchName string, distBranchDir string) (
	outMerge, outDiff []string, err error) {

	cmdMergeStr := fmt.Sprintf(cmdMerge, fmt.Sprintf("%s/%s", label, srcBranchName))
	outMerge, err = shellUtils.ExeWithOutput(cmdMergeStr, distBranchDir)
	if err != nil {
		logUtils.Errorf(i118Utils.Sprintf("fail_to_execute_cmd", cmdMergeStr, err.Error()))
	}

	msg := strings.Join(outMerge, "\n")
	if strings.Index(msg, "conflict") > -1 || strings.Index(msg, "CONFLICT") > -1 {
		err = errors.New("Merge Conflict: " + msg)
	}

	return
}

func (s *ScmService) GetDiffInfo(repoUrl, srcBranch, distBranch, distDir string) (out []string, err error) {
	distDir = fileUtils.RemoveLastSep(distDir) + "-diff"

	fileUtils.RmDir(distDir)

	// checkout distBranch
	s.CheckoutBranch(repoUrl, distBranch, distDir)

	// checkout srcBranch
	cmdCheckoutStr := fmt.Sprintf(cmdCheckout, srcBranch)
	out, err = shellUtils.ExeWithOutput(cmdCheckoutStr, distDir)
	if err != nil {
		logUtils.Errorf(i118Utils.Sprintf("fail_to_execute_cmd", cmdCheckoutStr, err.Error()))
	}

	cmdDiff := fmt.Sprintf(cmdDiff, srcBranch, distBranch)
	out, err = shellUtils.ExeWithOutput(cmdDiff, distDir)
	if err != nil {
		logUtils.Errorf(i118Utils.Sprintf("fail_to_execute_cmd", cmdDiff, err.Error()))
	}

	return
}

func (s *ScmService) GetBranchName(dir string) (branchName, branchNameRemote string, err error) {
	out, err := shellUtils.ExeWithOutput(cmdGetBranch, dir)
	if err != nil {
		logUtils.Errorf(i118Utils.Sprintf("fail_to_execute_cmd", cmdGetBranch, err.Error()))
	}
	if len(out) < 1 {
		return
	}

	branchName = out[0]
	branchName = strings.TrimSpace(branchName)

	cmdStr := fmt.Sprintf(cmdGetRemoteBranch, branchName)
	out, err = shellUtils.ExeWithOutput(cmdStr, dir)
	if err != nil {
		logUtils.Errorf(i118Utils.Sprintf("fail_to_execute_cmd", cmdStr, err.Error()))
	}
	if len(out) < 1 {
		return
	}

	arr := strings.Split(out[0], "=")
	if len(arr) > 1 {
		branchNameRemote = arr[1]
	}
	branchNameRemote = strings.TrimSpace(branchNameRemote)

	return
}

func (s *ScmService) GetBrotherDir(base, name string) (dir string) {
	parentDir := fileUtils.GetParent(base)

	dir = filepath.Join(parentDir, name)
	dir = fileUtils.AbsoluteDir(dir)

	return
}
