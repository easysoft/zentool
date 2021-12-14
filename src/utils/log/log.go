package logUtils

import (
	"fmt"
	constant "github.com/easysoft/z/src/utils/const"
	"github.com/easysoft/z/src/utils/vari"
	"github.com/sirupsen/logrus"
	"path/filepath"
)

var Logger *logrus.Logger

func InitLogger() *logrus.Logger {
	vari.LogDir = filepath.Join(vari.ExeDir + constant.LogDir)

	Logger = logrus.New()

	return Logger
}

func Log(msg string) {
	Logger.Infoln(msg)
}

func Error(msg string) {
	Logger.Errorln(msg)
}

func Logf(msg string, params ...interface{}) {
	str := fmt.Sprintf(msg, params...)
	Logger.Infoln(str)
}
func Errorf(msg string, params ...interface{}) {
	str := fmt.Sprintf(msg, params...)
	Logger.Errorln(str)
}
