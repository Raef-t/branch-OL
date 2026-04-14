import '/features/auth/presentation/managers/models/user_model.dart';

class AuthModel {
  final String? token;
  final UserModel? userModel;
  AuthModel({required this.token, required this.userModel});
  factory AuthModel.fromJson({required Map<String, dynamic> json}) {
    return AuthModel(
      token: json['token'] as String?,
      userModel: json['user'] != null
          ? UserModel.fromJson(json: json['user'])
          : null,
    );
  }
}
