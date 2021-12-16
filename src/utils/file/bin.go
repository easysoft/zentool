package fileUtils

import (
	"bytes"
	"io"
	"os"
)

func ReadConfFromBin(filePath string) string {
	file, err := os.Open(filePath)
	if err != nil {
		os.Exit(1)
	}
	defer file.Close()

	bytes, _ := getBackwardLine(file, 0)

	return string(bytes)
}

func getBackwardLine(file *os.File, start int64) (lineBytes []byte, cursor int64) {
	cursor = start
	stat, _ := file.Stat()
	filesize := stat.Size()

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

		if char[0] == 0 {
			zeroCount++
			if zeroCount >= 7 {
				break
			}
		}

		if cursor == -filesize {
			break
		}
	}

	lineBytes = bytes.Trim(lineBytes, "\x00")

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
