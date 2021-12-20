package logUtils

import (
	"encoding/json"
	"fmt"
	"github.com/easysoft/z/src/utils/common"
	"github.com/easysoft/z/src/utils/const"
	i118Utils "github.com/easysoft/z/src/utils/i118"
	"github.com/fatih/color"
	"os"
	"regexp"
	"strings"
)

var (
	usageFile  = fmt.Sprintf("res%sdoc%susage.txt", string(os.PathSeparator), string(os.PathSeparator))
	sampleFile = fmt.Sprintf("res%sdoc%ssample.txt", string(os.PathSeparator), string(os.PathSeparator))
)

func PrintUsage() {
	PrintToWithColor(i118Utils.Sprintf("usage"), color.FgCyan)

	usage := commonUtils.ReadResData(usageFile)
	fmt.Printf("%s\n", usage)

	PrintToWithColor("\n"+i118Utils.Sprintf("example"), color.FgCyan)
	sample := commonUtils.ReadResData(sampleFile)
	if !commonUtils.IsWin() {
		regx, _ := regexp.Compile(`\\`)
		sample = regx.ReplaceAllString(sample, "/")

		regx, _ = regexp.Compile(constant.AppName + `.exe`)
		sample = regx.ReplaceAllString(sample, constant.AppName)
	}
	fmt.Printf("%s\n", sample)
}

func PrintToWithColor(msg string, attr color.Attribute) {
	output := color.Output

	if attr == -1 {
		fmt.Fprint(output, msg+"\n")
	} else {
		color.New(attr).Fprintf(output, msg+"\n")
	}
}

func ConvertUnicode(str []byte) string {
	var a interface{}

	temp := strings.Replace(string(str), "\\\\", "\\", -1)

	err := json.Unmarshal([]byte(temp), &a)

	var msg string
	if err == nil {
		msg = fmt.Sprint(a)
	} else {
		msg = temp
	}

	return msg
}
