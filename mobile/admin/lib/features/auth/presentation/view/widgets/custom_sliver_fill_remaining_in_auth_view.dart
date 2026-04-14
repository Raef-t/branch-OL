import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/features/auth/presentation/view/widgets/custom_forget_password_in_auth_view.dart';
import '/features/auth/presentation/view/widgets/custom_header_section_in_auth_view.dart';
import '/features/auth/presentation/view/widgets/custom_register_card_button_in_auth_view.dart';
import '/features/auth/presentation/view/widgets/custom_register_text_in_auth_view.dart';
import '/features/auth/presentation/view/widgets/custom_two_fields_in_auth_view.dart';

class CustomSliverFillRemainingInAuthView extends StatelessWidget {
  const CustomSliverFillRemainingInAuthView({
    super.key,
    required this.formKey,
    required this.autovalidateMode,
    required this.nameTextEditingController,
    required this.passwordTextEditingController,
    required this.isChecked,
    this.onChanged,
    required this.loginOnTap,
  });
  final GlobalKey<FormState> formKey;
  final AutovalidateMode autovalidateMode;
  final TextEditingController nameTextEditingController,
      passwordTextEditingController;
  final bool isChecked;
  final void Function(bool?)? onChanged;
  final void Function() loginOnTap;
  @override
  Widget build(BuildContext context) {
    return SliverFillRemaining(
      hasScrollBody: false,
      child: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Color.fromRGBO(245, 178, 238, 0.2),
              Color(0xFFFFFFFF), // أبيض
            ],
            stops: [0.0, 0.45],
          ),
        ),
        child: Form(
          key: formKey,
          autovalidateMode: autovalidateMode,
          child: Stack(
            children: [
              Column(
                children: [
                  Column(
                    children: [
                      SizedBox(height: MediaQuery.sizeOf(context).height * 0.1),
                      const CustomHeaderSectionInAuthView(),
                    ],
                  ),
                  SizedBox(height: MediaQuery.sizeOf(context).height * 0.09),
                  Column(
                    children: [
                      const CustomRegisterTextInAuthView(),
                      Heights.height37(context: context),
                      CustomTwoFieldsInAuthView(
                        nameTextEditingController: nameTextEditingController,
                        passwordTextEditingController:
                            passwordTextEditingController,
                      ),
                    ],
                  ),
                  CustomForgetPasswordInAuthView(
                    isChecked: isChecked,
                    onChanged: onChanged,
                  ),
                ],
              ),
              Positioned(
                top: MediaQuery.sizeOf(context).height * 0.88,
                left: 0,
                right: 0,
                child: CustomRegisterCardButtonInAuthView(onTap: loginOnTap),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
