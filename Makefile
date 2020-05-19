run-tests:
	docker build -t client-tests . -q && docker run -t client-tests