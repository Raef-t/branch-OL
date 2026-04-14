import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';

Future<bool> userIsLoggedInHelper() async {
  final token = await StoreParametersInSharedPreferences.getStringParameter(
    key: keyTokenAuthToUserInSharedPreferences,
  );
  final isLoggedIn = token != null && token.isNotEmpty ? true : false;
  return isLoggedIn;
}
