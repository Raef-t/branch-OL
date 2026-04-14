import 'package:flutter/material.dart';
import '/core/helpers/get_scale_factor_helper.dart';
import '/features/auth/presentation/managers/models/user_model.dart';

class CustomProfileMissImageHomeView extends StatelessWidget {
  const CustomProfileMissImageHomeView({
    super.key,
    required this.userModel,
    required this.userPhoto,
  });
  final UserModel? userModel;
  final String userPhoto;
  @override
  Widget build(BuildContext context) {
    final imageSize = (35 * getScaleFactorHelper(context: context)).clamp(
      58.0,
      78.0,
    );
    return SizedBox(
      height: imageSize,
      width: imageSize,
      child: ClipOval(
        child: Image.network(
          userModel?.photo != null && userModel!.photo!.isNotEmpty
              ? userModel!.photo!
              : userPhoto,
          fit: BoxFit.fill,
          errorBuilder: (context, error, stackTrace) {
            return Image.network(
              'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTpR2mt4DTP5bMkhpMu1eMde4Rg6EFc78CfIg&s',
              fit: BoxFit.fill,
            );
          },
        ),
      ),
    );
  }
}
