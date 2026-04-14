import 'package:flutter/material.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/texts_style.dart';
import '/features/auth/presentation/managers/models/user_model.dart';
import '/gen/assets.gen.dart';

class CustomImageAndTextInAppBarHomeView extends StatelessWidget {
  const CustomImageAndTextInAppBarHomeView({
    super.key,
    required this.userModel,
    required this.userName,
  });
  final UserModel? userModel;
  final String userName;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        OnlyPaddingWithChild.bottom5(
          context: context,
          child: Assets.images.leftHandImage.image(),
        ),
        Widths.width11(context: context),
        Text(
          'مرحبا  ${(userModel?.name) ?? userName}',
          style: TextsStyle.medium18(context: context),
        ),
      ],
    );
  }
}
