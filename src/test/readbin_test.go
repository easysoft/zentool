package testing

import (
	fileUtils "github.com/easysoft/z/src/utils/file"
	logUtils "github.com/easysoft/z/src/utils/log"
	"testing"
)

func TestReadBin(t *testing.T) {
	logUtils.InitLogger()

	confStr := fileUtils.ReadConfFromBin("/Users/aaron/z")

	logUtils.Logf("%s", confStr)
}
