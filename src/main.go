package main

import (
	"flag"
	"github.com/easysoft/z/src/module"
	configUtils "github.com/easysoft/z/src/utils/config"
	constant "github.com/easysoft/z/src/utils/const"
	i118Utils "github.com/easysoft/z/src/utils/i118"
	"github.com/easysoft/z/src/utils/log"
	"github.com/easysoft/z/src/utils/vari"
	"github.com/facebookgo/inject"
	"github.com/fatih/color"
	"os"
	"os/signal"
	"syscall"
)

var (
	srcBranchDir   string
	distBranchName string
	language       string

	flagSet *flag.FlagSet
)

func main() {
	channel := make(chan os.Signal)
	signal.Notify(channel, os.Interrupt, syscall.SIGTERM)
	go func() {
		<-channel
		cleanup()
		os.Exit(0)
	}()

	flagSet = flag.NewFlagSet("z", flag.ContinueOnError)
	flagSet.StringVar(&srcBranchDir, "s", "./", "")
	flagSet.StringVar(&distBranchName, "d", "", "")
	flagSet.StringVar(&language, "l", string(constant.LanguageZH), "")
	flagSet.BoolVar(&vari.Verbose, "verbose", false, "")

	if len(os.Args) == 1 {
		os.Args = append(os.Args, "help", ".")
	}

	act := os.Args[1]

	flagSet.Parse(os.Args[2:])
	i118Utils.InitI118(language)
	configUtils.InitConfig(language)

	modules := module.NewModules()
	// inject objects
	var g inject.Graph
	if err := g.Provide(
		&inject.Object{Value: modules},
	); err != nil {
		logUtils.Errorf(i118Utils.Sprintf("inject_fail", err.Error()))
		return
	}
	if err := g.Populate(); err != nil {
		logUtils.Errorf(i118Utils.Sprintf("inject_fail", err.Error()))
		return
	}

	switch act {
	case "mr":
		if distBranchName == "" {
			logUtils.PrintUsage()
			return
		}
		modules.MergeAction.PreMerge(srcBranchDir, distBranchName)
	default:
		logUtils.PrintUsage()
	}
}

func init() {
	cleanup()
	logUtils.InitLogger()
}

func cleanup() {
	color.Unset()
}
