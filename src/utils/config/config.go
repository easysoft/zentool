package configUtils

import (
	fileUtils "github.com/easysoft/z/src/utils/file"
	"github.com/easysoft/z/src/utils/i118"
	"github.com/easysoft/z/src/utils/vari"
)

func InitConfig(language string) {
	vari.ExeDir, vari.IsDebug = fileUtils.GetExeDir()

	// internationalization
	i118Utils.InitI118(language)
}
