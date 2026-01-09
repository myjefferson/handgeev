import React from 'react'

export default class ErrorBoundary extends React.Component {
  constructor(props) {
    super(props)
    this.state = { hasError: false, error: null }
  }

  static getDerivedStateFromError(error) {
    return { hasError: true, error }
  }

  componentDidCatch(error, info) {
    console.error('Erro capturado:', error, info)
  }

  render() {
    if (this.state.hasError) {
      return <pre>{this.state.error?.toString()}</pre>
    }

    return this.props.children
  }
}