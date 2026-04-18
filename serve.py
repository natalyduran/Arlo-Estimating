"""
Simple dev server for Arlo Estimating.
Run: python3 serve.py
"""
import http.server
import socketserver

PORT = 8000
DIRECTORY = "/Users/natalyduran/Documents/Documents/Web dev/Arlo Estimating"


class Handler(http.server.SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, directory=DIRECTORY, **kwargs)

    def log_message(self, format, *args):
        print(f"  {self.address_string()} — {format % args}")


print(f"Serving Arlo Estimating at http://localhost:{PORT}")
with socketserver.TCPServer(("", PORT), Handler) as httpd:
    httpd.serve_forever()
