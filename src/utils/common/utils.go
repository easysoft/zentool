package commonUtils

import (
	"github.com/easysoft/z/res"
	"github.com/easysoft/z/src/utils/const"
	"io/ioutil"
	"os"
	"path"
	"path/filepath"
	"regexp"
	"runtime"
	"strings"
)

func Base(pathStr string) string {
	pathStr = filepath.ToSlash(pathStr)
	return path.Base(pathStr)
}

func RemoveBlankLine(str string) string {
	myExp := regexp.MustCompile(`\n{3,}`) // 连续换行
	ret := myExp.ReplaceAllString(str, "\n\n")

	ret = strings.Trim(ret, "\n")
	ret = strings.TrimSpace(ret)

	return ret
}

func GetOs() string {
	osName := runtime.GOOS

	if osName == "darwin" {
		return "mac"
	} else {
		return osName
	}
}
func IsWin() bool {
	return GetOs() == "windows"
}
func IsLinux() bool {
	return GetOs() == "linux"
}
func IsMac() bool {
	return GetOs() == "mac"
}

func AddSlashForUrl(url string) string {
	if strings.LastIndex(url, "/") < len(url)-1 {
		url += "/"
	}

	return url
}

func IgnoreFile(path string) bool {
	path = filepath.Base(path)

	if strings.Index(path, ".") == 0 ||
		path == "bin" || path == "release" || path == "logs" || path == "xdoc" {
		return true
	} else {
		return false
	}
}

func IsRelease() bool {
	arg1 := strings.ToLower(os.Args[0])
	name := filepath.Base(arg1)

	//log.Printf("%s, %s",arg1, name)

	return strings.Index(name, constant.AppName) == 0 && strings.Index(arg1, "go-build") < 0
}

func GetDebugParamForRun(args []string) (debug string, ret []string) {
	index := -1
	for i, item := range args {
		if item == "-debug" || item == "--debug" {
			index = i
			break
		}
	}

	if index > -1 && len(args) > index+1 {
		debug = args[index+1]
		ret = append(args[0:index], args[index+2:]...)
	} else {
		ret = args
	}

	return
}

func FileExist(path string) bool {
	var exist = true
	if _, err := os.Stat(path); os.IsNotExist(err) {
		exist = false
	}
	return exist
}

func ReadResData(path string) string {
	isRelease := IsRelease()

	var jsonStr string
	if isRelease {
		data, _ := res.Asset(path)
		jsonStr = string(data)
	} else {
		buf, _ := ioutil.ReadFile(path)
		str := string(buf)
		jsonStr = RemoveBlankLine(str)
	}

	return jsonStr
}
