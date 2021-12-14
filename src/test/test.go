package main

import (
	"fmt"
	scmService "github.com/easysoft/zentaoatf/src/service/scm"
)

func cleanup() {
	fmt.Println("cleanup")
}

func main() {
	scmService.MergeLocal("/Users/aaron/ci_test_testng", "b2")
}
