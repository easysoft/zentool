package main

import (
	"flag"
	configUtils "github.com/easysoft/z/src/utils/config"
	"github.com/easysoft/z/src/utils/log"
	"github.com/easysoft/z/src/utils/vari"
	"github.com/fatih/color"
	"os"
	"os/signal"
	"syscall"
)

var (
	targetBranch string

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
	flagSet.StringVar(&targetBranch, "t", "", "")
	flagSet.BoolVar(&vari.Verbose, "verbose", false, "")

	if len(os.Args) == 1 {
		os.Args = append(os.Args, "help", ".")
	}

	logUtils.Log("===" + os.Args[1])

	switch os.Args[1] {
	case "help", "-h", "-help", "--help":
		logUtils.PrintUsage()

	default: // run
		flagSet.Parse(os.Args[1:])

		if len(os.Args) > 1 {
			args := []string{os.Args[0], "run"}
			args = append(args, os.Args[1:]...)

			run(args)
		} else {
			logUtils.PrintUsage()
		}
	}
}

func run(args []string) {
	if len(args) >= 1 {

	} else {
		logUtils.PrintUsage()
	}
}

func init() {
	cleanup()
	configUtils.InitConfig()
	logUtils.InitLogger()
}

func cleanup() {
	color.Unset()
}
