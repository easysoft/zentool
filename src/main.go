package main

import (
	"flag"
	"github.com/easysoft/z/src/action"
	configUtils "github.com/easysoft/z/src/utils/config"
	constant "github.com/easysoft/z/src/utils/const"
	i118Utils "github.com/easysoft/z/src/utils/i118"
	"github.com/easysoft/z/src/utils/log"
	"github.com/easysoft/z/src/utils/vari"
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

	flagSet.Parse(os.Args[1:])
	i118Utils.InitI118(language)
	configUtils.InitConfig(language)

	switch os.Args[1] {
	case "help", "-h", "-help", "--help":
		logUtils.PrintUsage()

	default: // run
		action.PreMerge(srcBranchDir, distBranchName)
	}
}

func init() {
	cleanup()
	logUtils.InitLogger()
}

func cleanup() {
	color.Unset()
}
