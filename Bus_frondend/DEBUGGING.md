# Debugging Guide - Authentication Issues

## Common Issues and Solutions

### Issue: User gets logged out when clicking buttons

#### Symptoms:
- User logs in successfully
- Dashboard loads
- Clicking any button causes logout
- User is redirected to login page

#### Possible Causes:

1. **Missing Refresh Token**
   - Login response doesn't include `refresh_token`
   - Refresh token not stored correctly
   - **Solution**: Check browser console for "No refresh token available" warning
   - **Fix**: Ensure backend returns `refresh_token` in login response

2. **Token Refresh Endpoint Issues**
   - Refresh endpoint doesn't exist or returns error
   - Refresh token format is incorrect
   - **Solution**: Check browser console for "Token refresh failed" error
   - **Fix**: Verify `/api/v1/refresh` endpoint works correctly

3. **API Response Structure Mismatch**
   - Backend returns different structure than expected
   - **Solution**: Check browser console for API errors
   - **Fix**: Verify API response structure matches expected format

4. **Network Errors**
   - Backend is not running
   - CORS issues
   - Network connectivity problems
   - **Solution**: Check browser Network tab in DevTools
   - **Fix**: Verify backend is running and CORS is configured

## Debugging Steps

### 1. Check Authentication Status

Open browser console and run:
```javascript
window.checkAuthStatus()
```

This will show:
- Whether access token exists
- Whether refresh token exists
- Token expiry information
- User data

### 2. Check Browser Console

Look for these messages:
- `Login response:` - Shows what backend returns on login
- `Refresh token stored successfully` - Confirms refresh token was stored
- `No refresh token in login response` - Warning if refresh token missing
- `Attempting to refresh access token...` - When token refresh starts
- `Token refreshed successfully` - When refresh succeeds
- `Token refresh failed:` - When refresh fails (check error details)

### 3. Check Network Tab

1. Open DevTools (F12)
2. Go to Network tab
3. Filter by "Fetch/XHR"
4. Look for:
   - `/login` request - Check response for tokens
   - `/refresh` request - Check if it's called and what it returns
   - Other API requests - Check if they return 401

### 4. Check LocalStorage

1. Open DevTools (F12)
2. Go to Application tab
3. Check Local Storage
4. Look for:
   - `access_token` - Should exist after login
   - `refresh_token` - Should exist after login
   - `user` - Should contain user data

### 5. Test Token Refresh Manually

In browser console:
```javascript
// Get refresh token
const refreshToken = localStorage.getItem('refresh_token');

// Try to refresh
fetch('http://localhost:8000/api/v1/refresh', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({ refresh_token: refreshToken })
})
.then(r => r.json())
.then(console.log)
.catch(console.error);
```

## Expected API Response Formats

### Login Response
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "refresh_token": "def50200...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "role": {
      "id": 1,
      "name": "admin"
    }
  }
}
```

### Refresh Response
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "refresh_token": "def50200...", // Optional - only if rotated
  "token_type": "bearer",
  "expires_in": 3600
}
```

## Common Fixes

### Fix 1: Backend Not Returning Refresh Token

If backend doesn't return `refresh_token`:
1. Update backend to return refresh token in login response
2. Or disable token refresh (not recommended for production)

### Fix 2: Refresh Endpoint Not Working

If `/api/v1/refresh` doesn't work:
1. Check backend route is configured correctly
2. Verify endpoint accepts `refresh_token` in request body
3. Check backend logs for errors

### Fix 3: CORS Issues

If you see CORS errors:
1. Ensure backend allows requests from frontend origin
2. Check CORS headers in backend response
3. Verify `Access-Control-Allow-Credentials` is set if using cookies

### Fix 4: Token Expiry Too Short

If tokens expire too quickly:
1. Check token expiry time in backend
2. Consider increasing token lifetime
3. Ensure refresh happens before expiry

## Testing Checklist

- [ ] Login stores both access_token and refresh_token
- [ ] Token refresh works when access token expires
- [ ] API calls work after login
- [ ] Clicking buttons doesn't cause logout
- [ ] Dashboard loads data correctly
- [ ] Network errors don't cause logout
- [ ] Invalid tokens cause proper logout

## Still Having Issues?

1. Check browser console for specific errors
2. Check backend logs for API errors
3. Verify API endpoints are correct
4. Test with Postman/curl to verify backend works
5. Check network tab for failed requests
6. Verify environment variables are set correctly

