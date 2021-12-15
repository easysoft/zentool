package constant

type CIServerType string

const (
	Jenkins  CIServerType = "jenkins"
	GitLabCI CIServerType = "gitlab_ci"
)

func (e CIServerType) String() string {
	return string(e)
}
