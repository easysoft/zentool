package serverConst

type ResultCode int

const (
	ResultSuccess ResultCode = 1
	ResultFail    ResultCode = 0
)

func (c ResultCode) Int() int {
	return int(c)
}
