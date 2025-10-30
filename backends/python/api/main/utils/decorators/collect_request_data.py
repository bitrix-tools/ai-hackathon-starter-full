import json
from functools import wraps


def collect_request_data(view_func):
    """
    Decorator that collects GET and POST parameters into request.data
    Supports both single values and lists for parameters
    """

    @wraps(view_func)
    def wrapper(request, *args, **kwargs):
        try:
            params = json.loads(request.body.decode("utf-8"))
        except ValueError:
            params = {}

        request.data = params or {}

        # Process GET parameters
        for key in request.GET:
            values = request.GET.getlist(key)
            # If multiple values exist, store as list, else store single value
            request.data[key] = values if len(values) > 1 else values[0]

        # Process POST parameters
        for key in request.POST:
            values = request.POST.getlist(key)
            # POST parameters override GET parameters with same name
            # Store as list if multiple values, else single value
            request.data[key] = values if len(values) > 1 else values[0]

        return view_func(request, *args, **kwargs)

    return wrapper
