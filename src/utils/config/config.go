package configUtils

import (
	fileUtils "github.com/easysoft/z/src/utils/file"
	"github.com/easysoft/z/src/utils/i118"
	logUtils "github.com/easysoft/z/src/utils/log"
	"github.com/easysoft/z/src/utils/vari"
	"github.com/fatih/color"
	"os"
)

func InitConfig() {
	vari.ExeDir, vari.IsDebug = fileUtils.GetExeDir()
	CheckConfigPermission()

	// internationalization
	i118Utils.InitI118(vari.Config.Language)
}

func CheckConfigPermission() {
	//err := syscall.Access(vari.ExeDir, syscall.O_RDWR)

	err := fileUtils.MkDirIfNeeded(vari.ExeDir + "conf")
	if err != nil {
		logUtils.PrintToWithColor(
			i118Utils.Sprintf("perm_deny", vari.ExeDir), color.FgRed)
		os.Exit(0)
	}
}
