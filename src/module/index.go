package module

import (
	"github.com/easysoft/z/src/action"
)

type Modules struct {
	MergeAction *action.MergeAction `inject:""`
}

func NewModules() *Modules {
	return &Modules{}
}
