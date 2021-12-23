package fileUtils

import (
	"bytes"
	"errors"
	i118Utils "github.com/easysoft/z/src/utils/i118"
	logUtils "github.com/easysoft/z/src/utils/log"
	"io"
	"os"
)

func ReadConfFromBin(filePath string) (bytes []byte, err error) {
	file, err := os.Open(filePath)
	if err != nil {
		logUtils.Error(i118Utils.Sprintf("read_file_fail", filePath, err.Error()))
		return
	}
	defer file.Close()

	bytes, err = getBackwardLine(file, 0)

	return
}

func getBackwardLine(file *os.File, start int64) (lineBytes []byte, err error) {
	cursor := start
	stat, _ := file.Stat()
	filesize := stat.Size()

	totalCount := 0
	zeroCount := 0
	for {
		cursor--
		file.Seek(cursor, io.SeekEnd)

		char := make([]byte, 1)
		file.Read(char)

		//logUtils.Logf("%c, %s, %t", char[0], string(char[0]), char[0] == 0)
		if cursor != -1 && (char[0] == 10 || char[0] == 13) {
			break
		}

		lineBytes = append(lineBytes, char...)

		if char[0] == '~' {
			zeroCount++
			if zeroCount >= 16 {
				break
			}
		} else {
			zeroCount = 0
		}

		if cursor == -filesize {
			break
		}

		totalCount++
		if totalCount > 1000 {
			msg := "ERROR: CAN NOT FOUND CONFIG TERMINATOR"
			lineBytes = []byte(msg)
			err = errors.New(msg)
			return
		}
	}

	lineBytes = bytes.Trim(lineBytes, "~")

	lineBytes = reverse(lineBytes)

	return

}

func reverse(arr []byte) (ret []byte) {
	length := len(arr)
	for i := length - 1; i >= 0; i-- {
		ret = append(ret, arr[i])
	}

	return
}
