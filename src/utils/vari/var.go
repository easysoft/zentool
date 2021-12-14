package vari

import (
	"github.com/easysoft/zentaoatf/src/model"
)

var (
	IsDebug bool
	Config  = model.Config{}

	ExeDir string
	LogDir string

	ZenTaoVersion string
	SessionVar    string
	SessionId     string
	RequestType   string
	RequestFix    string

	Verbose bool
)
