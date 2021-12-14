package shellUtils

import (
	"bufio"
	"bytes"
	"fmt"
	commonUtils "github.com/easysoft/z/src/utils/common"
	logUtils "github.com/easysoft/z/src/utils/log"
	"io"
	"os/exec"
	"strings"
)

func ExeInDir(cmdStr, dir string) (string, error) {
	var cmd *exec.Cmd
	if commonUtils.IsWin() {
		cmd = exec.Command("cmd", "/C", cmdStr)
	} else {
		cmd = exec.Command("/bin/bash", "-c", cmdStr)
	}

	if dir != "" {
		cmd.Dir = dir
	}

	var out bytes.Buffer
	cmd.Stdout = &out

	err := cmd.Run()

	return out.String(), err
}

func ExeWithOutput(cmdStr, dir string) []string {
	var cmd *exec.Cmd
	if commonUtils.IsWin() {
		cmd = exec.Command("cmd", "/C", cmdStr)
	} else {
		cmd = exec.Command("/bin/bash", "-c", cmdStr)
	}

	if dir != "" {
		cmd.Dir = dir
	}

	output := make([]string, 0)

	stdout, err := cmd.StdoutPipe()

	if err != nil {
		fmt.Println(err)
		return output
	}

	cmd.Start()

	if err != nil {
		output = append(output, fmt.Sprint(err))
		return output
	}

	reader := bufio.NewReader(stdout)
	for {
		line, err2 := reader.ReadString('\n')
		if err2 != nil || io.EOF == err2 {
			break
		}
		logUtils.Log(strings.TrimRight(line, "\n"))
		output = append(output, line)
	}

	cmd.Wait()

	return output
}

func ExeSysCmd(cmdStr string) (string, error) {
	var cmd *exec.Cmd
	if commonUtils.IsWin() {
		cmd = exec.Command(cmdStr)
	} else {
		cmd = exec.Command("/bin/bash", "-c", cmdStr)
	}

	var out bytes.Buffer
	cmd.Stdout = &out

	err := cmd.Run()

	return out.String(), err
}
